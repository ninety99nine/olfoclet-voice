<template>

    <div class="relative">

        <Header></Header>

        <router-view></router-view>

        <Footer></Footer>

    </div>

</template>

<script>

    import Header from '@Layouts/website/components/Header.vue';
    import Footer from '@Layouts/website/components/Footer.vue';

    export default {
        components: {
            Header, Footer
        }
    };

</script>
