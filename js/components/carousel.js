document.addEventListener('DOMContentLoaded', function() {
    const track = document.querySelector('.carousel-track');
    const prevButton = document.querySelector('.carousel-button.prev');
    const nextButton = document.querySelector('.carousel-button.next');
    
    if (!track || !prevButton || !nextButton) return;

    let currentPosition = 0;
    const itemWidth = 300; // largeur de l'item + gap

    function updateButtonsVisibility() {
        prevButton.style.opacity = currentPosition === 0 ? '0.5' : '1';
        nextButton.style.opacity = currentPosition <= track.scrollWidth - track.clientWidth ? '0.5' : '1';
    }

    function scroll(direction) {
        const containerWidth = track.clientWidth;
        const scrollAmount = Math.min(containerWidth, itemWidth * 2);
        
        if (direction === 'prev') {
            currentPosition = Math.max(currentPosition - scrollAmount, 0);
        } else {
            currentPosition = Math.min(
                currentPosition + scrollAmount,
                track.scrollWidth - track.clientWidth
            );
        }

        track.scrollTo({
            left: currentPosition,
            behavior: 'smooth'
        });

        updateButtonsVisibility();
    }

    prevButton.addEventListener('click', () => scroll('prev'));
    nextButton.addEventListener('click', () => scroll('next'));

    // Mise à jour initiale des boutons
    updateButtonsVisibility();

    // Gestion du défilement tactile
    let touchStartX = 0;
    let touchEndX = 0;

    track.addEventListener('touchstart', e => {
        touchStartX = e.changedTouches[0].screenX;
    }, false);

    track.addEventListener('touchend', e => {
        touchEndX = e.changedTouches[0].screenX;
        if (touchStartX - touchEndX > 50) {
            scroll('next');
        } else if (touchEndX - touchStartX > 50) {
            scroll('prev');
        }
    }, false);
}); 