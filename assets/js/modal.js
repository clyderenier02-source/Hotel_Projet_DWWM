document.addEventListener("turbo:load", () => {

    const myBtn = document.querySelectorAll(".btn");

    myBtn.forEach(btn => {
        btn.addEventListener("click", () => {
            const modal = document.getElementById(btn.dataset.modal);

            modal.style.display = "block";
        })
    });

    const btnClose = document.querySelectorAll(".close");

    btnClose.forEach(close => {
        close.addEventListener("click", () => {
            const modalContent = close.parentElement.parentElement;

            modalContent.style.display = "none";
        })
    })
})