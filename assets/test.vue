<script>
export default {
    name: 'my-courses-coloured-table',
    props: ['prop'],
    data: () => ({ item: 'test' }),
</script>

<template>
 <div> {{ item }} <p>{{ prop }}</p></div>
</template>