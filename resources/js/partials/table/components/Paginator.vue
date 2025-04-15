<template>

    <ul class="inline-flex -space-x-px rtl:space-x-reverse text-sm h-8">
        <li v-for="(link, index) in pagination.links" :key="index">
            <a :href="link.url ?? '#'" @click.prevent="onClick(link)" :disabled="isDisabled(link)" :class="getLinkClass(index, link)">
                <span v-html="link.label"></span>
            </a>
        </li>

    </ul>

</template>

<script>
    /**
     * Component Reference: https://flowbite.com/docs/components/pagination/
     */
    export default {
        props: {
            pagination: {
                type: Object
            }
        },
        methods: {
            onClick(link) {
                if(link.url) {
                    this.$emit('paginate', link.url);
                }
            },
            isDisabled(link) {
                return link.url == null;
            },
            getLinkClass(index, link) {

                var classes;

                //  If first link
                if(index == 0) {

                    classes = 'flex items-center justify-center whitespace-nowrap px-3 h-8 ms-0 leading-tight border rounded-s-lg dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white';

                //  If last link
                }else if(index == this.pagination.links.length - 1) {

                    classes = 'flex items-center justify-center whitespace-nowrap px-3 h-8 leading-tight border rounded-e-lg hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white';

                //  If in-between link
                }else{

                    classes = 'flex items-center justify-center whitespace-nowrap px-3 h-8 leading-tight border dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white';

                }

                if(this.isDisabled(link)) {

                    classes += ' cursor-not-allowed bg-gray-100';

                }else{

                    classes += (link.active ? ' bg-blue-500 hover:bg-blue-500 border-blue-500 text-white' : 'bg-white hover:bg-gray-100 hover:text-gray-700 text-gray-500');

                }

                return classes;
            }
        }
    };
</script>
