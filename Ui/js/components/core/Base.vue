<script>
    export default {

        methods: {
            warn(text = "-") {
                console.warn(`🐞UiCore: ${text}`);
            },
            hasSlot(name = 'default') {
                return !!this.$slots[name] || !!this.$scopedSlots[name];
            }
        }
    };
</script>