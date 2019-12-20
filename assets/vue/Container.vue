<template>
    <div>
        <h1>Welcome to the fridget platform!</h1>
        <router-view></router-view>
        <br>
        <img src="https://media2.giphy.com/media/P7h0RFsX6HRx6/source.gif">
    </div>
</template>

<script>
    export default {
        name: "Home"
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
  @import 'assets/scss/style';
</style>
