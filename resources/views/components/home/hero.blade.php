<section class="relative bg-gray-200 dark:bg-gray-900 bg-cover bg-center"
    style="background-image: url('{{ asset('img/hero-bg.webp') }}');">
    <div class="absolute z-10 h-full w-full bg-white/65 dark:bg-gray-950/80"></div>
    <div class="relative z-20 mx-auto max-w-screen-xl px-4 py-14 text-center lg:px-12 lg:py-28">
        <h1
            class="mb-4 text-4xl font-extrabold leading-none tracking-tight text-gray-900 dark:text-white md:text-5xl lg:text-6xl">
            Samarkand Institute of Economics and Service
        </h1>
        <p
            class="my-10 rounded-2xl bg-gray-900/70 py-5 text-lg font-normal text-gray-100 shadow-md dark:bg-gray-100/80 dark:text-gray-800 sm:px-16 lg:text-xl xl:px-40">
            The Samarkand Institute of Economics and Service is a leading educational institution in Uzbekistan,
            dedicated to providing high-quality education in economics and service management. Established in 1992,
            the institute has a rich history of academic excellence and innovation.
        </p>
        <div class="mb-8 flex flex-col space-y-4 sm:flex-row sm:justify-center sm:space-x-4 sm:space-y-0 lg:mb-16">
            <x-button>
                <a href="#" class="inline-flex items-center justify-center">
                    News
                    <svg class="-mr-1 ml-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z"
                            clip-rule="evenodd"></path>
                    </svg>
                </a>
            </x-button>
            <x-button>
                <a href="#" class="inline-flex items-center justify-center">
                    <img src="/img/icons/014-graduation-hat.webp" alt="Icon" class="me-2 w-5 dark:invert" />
                    Faculity
                </a>
            </x-button>
        </div>
    </div>
</section>
