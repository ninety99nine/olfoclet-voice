export default {
    mounted(el) {
        el.textContent = el.textContent.charAt(0).toUpperCase() + el.textContent.slice(1);
    },
    updated(el) {
        el.textContent = el.textContent.charAt(0).toUpperCase() + el.textContent.slice(1);
    }
};
