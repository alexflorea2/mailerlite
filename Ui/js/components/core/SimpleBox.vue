<template>
    <div class="card shadow h-100 py-2" :class="`border-left-${cType}`">
        <div class="card-body">
            <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                        <slot v-if="hasSlot('title')" name="title"></slot>
                        <template v-else>
                            {{title}}
                        </template>
                    </div>
                    <div class="mb-0 font-weight-bold text-gray-800">
                        <slot v-if="hasSlot('body')" name="body"></slot>
                        <template v-else>
                            {{body}}
                        </template>
                    </div>
                </div>
                <div class="col-auto" v-if="icon">
                    <fa :icon="icon" class="fa-2x text-gray-300"/>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import BaseUiComponent from './Base';

    const ACCEPTED_TYPES = ['primary', 'success', 'info', 'danger', 'warning'];
    export default {
        name: "simple-box",
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
            },
            icon: {
                type: String,
                default: null
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