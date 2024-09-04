<template>
    <the-header></the-header>
    <the-form></the-form>
    <the-footer></the-footer>
</template>

<script>
import TheHeader from './Layouts/TheHeader.vue';
import TheFooter from './Layouts/TheFooter.vue';
import TheForm from './Pages/TheForm.vue';

export default {
    components: { TheHeader, TheFooter, TheForm }
}
</script>