export default {
    mounted(el) {
        el.textContent = el.textContent
            .split(' ')
            .map(word => word.charAt(0).toUpperCase() + word.slice(1))
            .join(' ');
    },
    updated(el) {
        el.textContent = el.textContent
            .split(' ')
            .map(word => word.charAt(0).toUpperCase() + word.slice(1))
            .join(' ');
    }
};
