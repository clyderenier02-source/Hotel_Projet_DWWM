document.addEventListener("turbo:load", () => {

    const myBtn = document.querySelectorAll(".btn");

    myBtn.forEach(btn => {
        btn.addEventListener("click", () => {
            const modal = document.getElementById(btn.dataset.modal);
            
            if(modal) {
                modal.style.display = "block";
            }
        })
    });

    const btnClose = document.querySelectorAll(".modal__close");

    btnClose.forEach(close => {
        close.addEventListener("click", () => {
            const modalContent = close.parentElement.parentElement;

            modalContent.style.display = "none";
        })
    })
})