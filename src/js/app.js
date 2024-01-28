document.addEventListener('DOMContentLoaded', () => {
    eventListeners()
})

function eventListeners() {
    const mobileMenu = document.querySelector('.mobile-menu')
    mobileMenu.addEventListener('click', navegacionResponsive)
}

function navegacionResponsive() {
    const navegacion = document.querySelector('.navegacion')

    if (navegacion.classList.contains('mostrar')) {
        navegacion.classList.remove('mostrar')
    } else {
        navegacion.classList.add('mostrar')
    }

    // tambien se puede hacer con un toggle
    // navegacion.classList.toggle('mostrar')

}