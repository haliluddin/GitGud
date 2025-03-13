document.addEventListener('DOMContentLoaded', function () {
    const sections = document.querySelectorAll('.tpdiv');

    sections.forEach((section) => {
        const rightFilter = section.querySelector('.rightfilter');
        const leftArrow = section.querySelector('.left-arrow');
        const rightArrow = section.querySelector('.right-arrow');
        const scrollAmount = 100; // Adjust scroll distance as needed

        // Check the scroll position to toggle arrow visibility
        function updateArrows() {
            leftArrow.style.display = rightFilter.scrollLeft > 0 ? 'flex' : 'none';
            rightArrow.style.display =
                rightFilter.scrollLeft < rightFilter.scrollWidth - rightFilter.clientWidth
                    ? 'flex'
                    : 'none';
        }

        // Scroll right when the right arrow is clicked
        rightArrow.addEventListener('click', () => {
            rightFilter.scrollBy({ left: scrollAmount, behavior: 'smooth' });
        });

        // Scroll left when the left arrow is clicked
        leftArrow.addEventListener('click', () => {
            rightFilter.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
        });

        // Update arrows on initial load and after scrolling
        updateArrows();
        rightFilter.addEventListener('scroll', updateArrows);
    });
});
