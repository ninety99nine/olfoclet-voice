export default {
    mounted(el) {
        el.addEventListener('input', (event) => {
            // Remove any non-numeric characters
            event.target.value = event.target.value.replace(/\D/g, '');

            // Ensure v-model gets updated correctly
            event.target.dispatchEvent(new Event('input'));
        });
    }
};
