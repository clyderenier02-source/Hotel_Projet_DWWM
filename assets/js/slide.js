document.addEventListener('turbo:load', () => {

    const gallery = document.querySelector(".roomGallery__wrapper");
    const btnSuivant = document.getElementById("suivant");
    const btnPrecedent = document.getElementById("precedent");

    const images = document.querySelectorAll(".roomGallery__wrapper img");

    let index = 0;

    if(btnSuivant && btnPrecedent && gallery && images.length > 0){

        btnSuivant.addEventListener("click", () => {

            if(index < images.length - 1){
                index++;
            }

            const width = images[0].offsetWidth;
            gallery.style.transform = `translateX(-${index * width}px)`;

        });

        btnPrecedent.addEventListener("click", () => {

            if(index > 0){
                index--;
            }

            const width = images[0].offsetWidth;
            gallery.style.transform = `translateX(-${index * width}px)`;

        });
    }
});
