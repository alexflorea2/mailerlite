<template>
    <div class="card text-white shadow" :class="`bg-${cType}`">
        <div class="card-body">
            <slot v-if="hasSlot('title')" name="title"></slot>
            <template v-else>
                {{title}}
            </template>
            <div class="text-white-50 small">
                <slot v-if="hasSlot('body')" name="body"></slot>
                <template v-else>
                    {{body}}
                </template>
            </div>
        </div>
    </div>
</template>

<script>
    import BaseUiComponent from './Base';

    const ACCEPTED_TYPES = ['primary', 'success', 'info', 'danger', 'warning', 'secondary'];
    export default {
        name: "alert",
        extends: BaseUiComponent,
        props: {
            title: {
                type: String,
                default: 'title content: either pass title as prop or as named slot'
            },
            body: {
                type: String,
                default: 'body content: either pass body as prop or as named slot'
            },
            type: {
                type: String,
                default: 'primary'
            }
        },

        computed: {

            cType() {
                if (ACCEPTED_TYPES.indexOf(this.type) !== -1) {
                    return this.type;
                }

                this.warn(`prop type=${this.type} passed to component ${this.$options.name} not in accepted values ${ACCEPTED_TYPES}`);
                return 'primary';
            }
        }
    };
</script>