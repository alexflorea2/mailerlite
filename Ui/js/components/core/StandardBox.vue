<template>
    <div class="card">
        <div class="card-header">
            <slot v-if="hasSlot('title')" name="title"></slot>
            <template v-else>
                {{title}}
            </template>
        </div>
        <div class="card-body">
            <slot v-if="hasSlot('body')" name="body"></slot>
            <template v-else>
                {{body}}
            </template>
        </div>
    </div>
</template>

<script>
    import BaseUiComponent from './Base';

    export default {
        name: "standard-box",
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
        }
    };
</script>