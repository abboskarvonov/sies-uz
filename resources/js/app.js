import "./bootstrap";
import { Livewire, Alpine } from "../../vendor/livewire/livewire/dist/livewire.esm";
import Intersect from "@alpinejs/intersect";
import Focus from "@alpinejs/focus";

Alpine.plugin(Intersect);
Alpine.plugin(Focus);
window.Alpine = Alpine;
Livewire.start();

// Lazy image skeleton: remove animation after load
document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll('img[loading="lazy"]').forEach((img) => {
        if (img.complete) {
            img.classList.add("img-loaded");
        } else {
            img.addEventListener("load", () => img.classList.add("img-loaded"), {
                once: true,
            });
            img.addEventListener("error", () => img.classList.add("img-loaded"), {
                once: true,
            });
        }
    });
});
