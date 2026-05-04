<?php
/**
 * Git webhook handler — GitHub & GitLab
 * URL: https://sies.uz/webhook.php
 *
 * GitHub:  Payload URL → https://sies.uz/webhook.php
 *          Content type → application/json
 *          Secret       → WEBHOOK_SECRET (.env qiymati)
 *          Events       → Just the push event
 *
 * GitLab:  URL          → https://sies.uz/webhook.php
 *          Secret token → WEBHOOK_SECRET (.env qiymati)
 *          Trigger      → Push events
 */

define('PROJECT_ROOT', dirname(__DIR__));
define('LOG_FILE',     PROJECT_ROOT . '/storage/logs/webhook.log');
define('LOCK_FILE',    PROJECT_ROOT . '/storage/framework/cache/deploy.lock');
define('DEPLOY_BRANCH', 'main');

// --------------------------------------------------------------------------
// Yordamchi funksiyalar
// --------------------------------------------------------------------------

function logMsg(string $msg): void
{
    $line = '[' . date('Y-m-d H:i:s') . '] ' . $msg . PHP_EOL;
    file_put_contents(LOG_FILE, $line, FILE_APPEND | LOCK_EX);
}

function respond(int $code, string $message): never
{
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode(['status' => $message, 'time' => date('c')]);
    exit;
}

function readEnvSecret(): string
{
    $envFile = PROJECT_ROOT . '/.env';
    if (!is_readable($envFile)) {
        return '';
    }
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with($line, 'WEBHOOK_SECRET=')) {
            return trim(substr($line, 15), " \t\n\r\0\x0B\"'");
        }
    }
    return '';
}

// --------------------------------------------------------------------------
// Faqat POST
// --------------------------------------------------------------------------

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(405, 'method_not_allowed');
}

$payload = file_get_contents('php://input');
$headers = array_change_key_case(getallheaders(), CASE_LOWER);
$secret  = readEnvSecret();

if (empty($secret)) {
    logMsg('ERROR: WEBHOOK_SECRET .env da topilmadi');
    respond(500, 'server_misconfigured');
}

// --------------------------------------------------------------------------
// Signature tekshirish
// --------------------------------------------------------------------------

$githubSig = $headers['x-hub-signature-256'] ?? '';
$gitlabToken = $headers['x-gitlab-token'] ?? '';

if ($githubSig) {
    // GitHub: HMAC-SHA256
    $expected = 'sha256=' . hash_hmac('sha256', $payload, $secret);
    if (!hash_equals($expected, $githubSig)) {
        logMsg('WARN: Noto\'g\'ri GitHub signature — ' . ($_SERVER['REMOTE_ADDR'] ?? ''));
        respond(401, 'invalid_signature');
    }
} elseif ($gitlabToken) {
    // GitLab: plain token
    if (!hash_equals($secret, $gitlabToken)) {
        logMsg('WARN: Noto\'g\'ri GitLab token — ' . ($_SERVER['REMOTE_ADDR'] ?? ''));
        respond(401, 'invalid_token');
    }
} else {
    logMsg('WARN: Signature/token yo\'q — ' . ($_SERVER['REMOTE_ADDR'] ?? ''));
    respond(401, 'unauthorized');
}

// --------------------------------------------------------------------------
// Branch tekshirish
// --------------------------------------------------------------------------

$data = json_decode($payload, true);
$ref  = $data['ref'] ?? ($data['commits'][0]['ref'] ?? '');

if ($ref !== 'refs/heads/' . DEPLOY_BRANCH) {
    logMsg("INFO: Branch ignored: {$ref}");
    respond(200, 'ignored');
}

// --------------------------------------------------------------------------
// Concurrent deploy oldini olish
// --------------------------------------------------------------------------

if (file_exists(LOCK_FILE) && (time() - filemtime(LOCK_FILE)) < 600) {
    logMsg('INFO: Deploy allaqachon ketmoqda, o\'tkazib yuborildi');
    respond(200, 'already_deploying');
}

// --------------------------------------------------------------------------
// Webhook ga darhol javob qaytarish, keyin deploy ishga tushirish
// --------------------------------------------------------------------------

$commit = substr($data['after'] ?? 'unknown', 0, 7);
$pusher = $data['pusher']['name'] ?? $data['user_username'] ?? 'unknown';
logMsg("INFO: Deploy boshlandi — commit={$commit} pusher={$pusher}");

http_response_code(200);
header('Content-Type: application/json');
echo json_encode(['status' => 'deploying', 'commit' => $commit, 'time' => date('c')]);

// Response ni darhol jo'natish (nginx/php-fpm bilan ishlaydi)
if (function_exists('fastcgi_finish_request')) {
    fastcgi_finish_request();
} else {
    if (ob_get_level()) ob_end_flush();
    flush();
}

// --------------------------------------------------------------------------
// Background deploy
// --------------------------------------------------------------------------

$logFile  = LOG_FILE;
$lockFile = LOCK_FILE;
$root     = PROJECT_ROOT;
$branch   = DEPLOY_BRANCH;

$cmd = implode(' && ', [
    "cd {$root}",
    "touch {$lockFile}",
    "git config --global --add safe.directory {$root}",
    "git fetch --prune origin",
    "git reset --hard origin/{$branch}",
    "bash deploy.sh",
]) . "; rm -f {$lockFile}";

// Lock ni tozalash ham error bo'lsa ham bajarilsin
$fullCmd = "nohup bash -c " . escapeshellarg($cmd) . " >> {$logFile} 2>&1 &";
exec($fullCmd);
