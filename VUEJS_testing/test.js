Vue.component('Cats', {
  props: ['msg'],
  template: '<h3>{{ msg }}</h3>'
});

// Template coming from PHP
const template = `<div id="main Content"><Cats msg="Meow... Meow..."></div>`

var app = new Vue({
  el: '#app',
  data: {
    compiled: null
  },
  mounted() {
    setTimeout(() => {
        this.compiled = Vue.compile(template);
    })
  }
});