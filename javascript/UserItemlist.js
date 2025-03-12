console.log('UserItemlist.js is loaded');

document.addEventListener('DOMContentLoaded', () => {
    // Get all item images
    const itemImages = document.querySelectorAll('.item-image');

    // Create lightbox elements
    const lightbox = document.createElement('div');
    lightbox.className = 'lightbox';
    const lightboxImage = document.createElement('img');
    const closeButton = document.createElement('span');
    closeButton.className = 'close';
    closeButton.textContent = '×'; // Unicode character for 'x'
    
    lightbox.appendChild(closeButton);
    lightbox.appendChild(lightboxImage);
    document.body.appendChild(lightbox);

    // Function to open lightbox
    function openLightbox(imageSrc) {
        lightboxImage.src = imageSrc;
        lightbox.style.display = 'flex';
    }

    // Function to close lightbox
    function closeLightbox() {
        lightbox.style.display = 'none';
    }

    // Event listeners for opening the lightbox on double-click
    itemImages.forEach(image => {
        image.addEventListener('dblclick', () => {
            openLightbox(image.src);
        });
    });

    // Event listener for closing the lightbox
    closeButton.addEventListener('click', () => {
        closeLightbox();
    });

    // Close lightbox if clicked outside the image
    lightbox.addEventListener('click', (event) => {
        if (event.target === lightbox) {
            closeLightbox();
        }
    });
});
