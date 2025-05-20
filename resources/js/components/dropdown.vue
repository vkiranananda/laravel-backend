<script>
export default {
    name: "dropdown",
    mounted() {
        // Что бы клик при активации элемента не отрабатывал.
        setTimeout(() => {
            document.addEventListener('click', this.handleClickOutside)
        }, 100)
    },
    beforeUnmount() {
        document.removeEventListener('click', this.handleClickOutside);
    },
    methods: {
        handleClickOutside(event) {
            const dropdown = this.$refs.dropdown;
            if (dropdown && !dropdown.contains(event.target)) this.$emit('v-click-outside')
        }
    }
}
</script>

<template>
    <div class="dropdown" ref="dropdown">
        <slot></slot>
    </div>
</template>

<style lang="scss">
.dropdown {
    border: 1px solid lightgray;
    background-color: white;
    padding: 5px;
    z-index: 100;
    position: relative;

    hr {
        margin: 5px 0;
    }

    .item {
        cursor: pointer;
        padding: 5px;

        &:hover {
            background-color: lightgray;
        }

        &.disabled {
            pointer-events: none;
            opacity: 0.5;
            cursor: not-allowed;
        }
    }
}
</style>
