<template>
    <div class="row mt-4">
        <div class="col-12">
            <label for="text_template" class="main-label">Текст шаблона</label>
            <div class="line-template-seo-props">
                <div @click="insertData('#NAME#')">
                    Название
                </div>
                <div @click="insertData('#PRICE#')">
                    Цена
                </div>
                <div v-for="(property, index) in properties"
                     :key="index"
                     @click="insertData('#PROP_'+property.id+'#')"
                >
                    {{ property.name }}
                </div>
            </div>
            <textarea
                type="text"
                name="text_template"
                id="text_template"
                class="form-control"
                autocomplete="off"
                v-model="textTemplate"
            />
        </div>
    </div>
</template>

<script>

import axios from "axios";

export default {
    name: "SeoTemplateComponent",
    props: {
        textTemplateProp: {
            type: String
        },
        properties: {
            type: Object
        }
    },
    data() {
        return {
            textTemplate: ''
        }
    },
    mounted() {
        if (this.textTemplateProp) {
            this.textTemplate = this.textTemplateProp
        }
    },


    methods: {
        insertData(element) {
            this.textTemplate = this.textTemplate + element
        }
    },
}
</script>

<style scoped lang="scss">
.line-template-seo-props div {
    display: inline-block;
    cursor: pointer;
    margin-right: 10px;
    padding: 7px;
    background: #ccc;
}
.line-template-seo-props {
    display: block;
    margin-bottom: 15px;
}
</style>
