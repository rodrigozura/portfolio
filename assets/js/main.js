(() => {
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (!window.gsap) {
        return;
    }

    if (window.ScrollTrigger) {
        gsap.registerPlugin(ScrollTrigger);
    }

    gsap.defaults({
        ease: 'power3.out',
        duration: reduceMotion ? 0 : 0.8,
    });

    const heroTargets = ['.hero-subtitle', '.hero-actions', '.hero-bottom-bar'];

    if (reduceMotion) {
        gsap.set('.hero-name', { autoAlpha: 1, y: 0, clearProps: 'visibility,opacity,transform' });
        gsap.set(heroTargets, { autoAlpha: 1, y: 0 });
        gsap.set('.about, .publications', { '--separator-scale': 1 });
        return;
    }

    gsap.set('.hero-name', { autoAlpha: 1, y: 0, clearProps: 'visibility,opacity,transform' });

    gsap.from(heroTargets, {
        autoAlpha: 0,
        y: 18,
        stagger: 0.12,
        duration: 0.9,
        clearProps: 'visibility,opacity,transform',
    });

    if (!window.ScrollTrigger) {
        return;
    }

    gsap.set('.about, .publications', { '--separator-scale': 0 });

    gsap.utils.toArray('.about, .publications').forEach((section) => {
        gsap.to(section, {
            '--separator-scale': 1,
            duration: 0.72,
            ease: 'power3.inOut',
            scrollTrigger: {
                trigger: section,
                start: 'top 78%',
                once: true,
            },
        });
    });

    ScrollTrigger.batch('.section-headline, .about-text, .about-photo, .pub-card', {
        start: 'top 84%',
        once: true,
        onEnter: (elements) => {
            gsap.from(elements, {
                autoAlpha: 0,
                y: 22,
                stagger: 0.08,
                duration: 0.72,
                overwrite: true,
            });
        },
    });
})();
