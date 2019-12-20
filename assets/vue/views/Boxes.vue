<template>
    <div>
        <div class="row col">
            <h1>Please select a box</h1>
        </div>

        <router-link :to="{ name: 'Dashboard', params: { boxId: 1 }}">Box 1</router-link>
        <router-link :to="{ name: 'Dashboard', params: { boxId: 2 }}">Box 2</router-link>
        <router-link :to="{ name: 'Dashboard', params: { boxId: 3 }}">Box 3</router-link>
    </div>
</template>

<script>
    export default {
        name: "Boxes"
    };
</script>

<style lang="scss">
  // CoreUI Icons Set
  @import '~@coreui/icons/css/coreui-icons.min.css';
  /* Import Font Awesome Icons Set */
  $fa-font-path: '~font-awesome/fonts/';
  @import '~font-awesome/scss/font-awesome.scss';
  /* Import Simple Line Icons Set */
  $simple-line-font-path: '~simple-line-icons/fonts/';
  @import '~simple-line-icons/scss/simple-line-icons.scss';
  /* Import Flag Icons Set */
  @import '~flag-icon-css/css/flag-icon.min.css';
  /* Import Bootstrap Vue Styles */
  @import '~bootstrap-vue/dist/bootstrap-vue.css';
  // Import Main styles for this application
  @import '../assets/scss/style';
</style>
