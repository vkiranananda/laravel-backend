<template>
    <div class="autocomplete">
        <input
            type="text"
            ref="input"
            :placeholder="field.placeholder"
            v-model="value"
            class="form-control"
            @click="openResults"
            autocorrect="off"
            autocomplete="off"
            autocapitalize="off"
            spellcheck="false"
            :readonly="field.readonly"
            @keyup.enter="select"
            @keyup.down="down"
            @keyup.up.prevent.stop="up($event)"
        >
        <div v-show="showResults" class="results text-secondary rounded-bottom" ref="results">
            <div v-for="(el, key) in searchData" :key="key" class="item p-2" :class="focus.value == el.value ? 'bg-light' : ''"
                 @mouseover="mouseover(el)"
                 @click="select"
            >
                {{ el.label }}
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['field'],
        data() {
            return {
                // Найденые элементы
                searchData: [],
                // Открыт ли список
                showResults: false,
                resultsUserClose: false,
                // Выставлем true когда не нужно делать запрос на сервер при спене value
                closeResults: false,
                value: this.field.value,
                timerId: 0,
                focus: {}
            }
        },

        watch: {
            // Подключаем обработчик клика мышки вне элемента
            showResults: function (value, oldValue) {
                if (value == true) {
                    document.body.addEventListener('click', this.clickOutside)
                    this.resultsUserClose = false
                } else document.body.removeEventListener('click', this.clickOutside)
            },
            value: function (value, oldValue) {
                this.$emit('change', value)
                if (value.length < 3 || this.closeResults) {
                    this.showResults = false
                    this.closeResults = false
                    return
                }
                clearTimeout(this.timerId)
                this.timerId = setTimeout(() => {
                    axios.post(this.field.url, {value})
                        .then((response) => {
                            this.searchData = response.data
                            if (this.searchData.length > 0) {
                                // Если фокус уже есть
                                this.showResults = true
                                if (this.focus.value) {
                                    let i = this.searchItem(this.focus.value)
                                    // если элемент есть в списке фокус не меняем
                                    if (i !== false) return
                                }
                                // Иначе присваиваем фокус первому элементу
                                this.focus = this.searchData[0]
                            } else {
                                this.showResults = false
                                this.focus = {}
                            }
                        })
                        .catch((error) => {
                            console.log(error.response)
                        })
                }, 170)
            },
        },
        methods: {
            clickOutside: function (event) {
                if (!(this.$refs.results == event.target || this.$refs.results.contains(event.target)
                    || this.$refs.input == event.target || this.$refs.input.contains(event.target))
                ) {
                    this.showResults = false
                    this.resultsUserClose = true
                }
            },
            openResults: function () {
                if (this.resultsUserClose) {
                    this.showResults = true
                }
            },
            mouseover: function (el) {
                 this.focus = el
            },
            select: function () {
                if (this.showResults) {
                    // Закрываем список если значения равны
                    if (this.focus.value == this.value) {
                        this.showResults = false

                    } else {
                        // Отменяем запрос по аякс
                        this.closeResults = true
                        this.value = this.focus.value
                    }
                }
            },
            // Поиск значения внутри массива
            searchItem: function (search) {
                for (let i in this.searchData) if (this.searchData[i].value == search) return parseInt(i)
                return false
            },
            // Клавиша вниз
            down: function (event) {
                let i = this.searchItem(this.focus.value)
                if (i !== false && this.searchData[i + 1]) {
                    this.focus = this.searchData[i + 1]
                }
                // event.preventDefault();
            },
            //Клавиша вверх
            up: function (event) {
                let i = this.searchItem(this.focus.value)
                if (i > 0 && this.searchData[i - 1]) {
                    this.focus = this.searchData[i - 1]
                    // event.preventDefault()
                    this.endCursor()
                }
            },
            endCursor: function () {
                let el = this.$refs['input']
                if (typeof el.selectionStart == "number") {
                    el.selectionStart = el.selectionEnd = el.value.length;
                } else if (typeof el.createTextRange != "undefined") {
                    el.focus();
                    var range = el.createTextRange();
                    range.collapse(false);
                    range.select();
                }
            }

        }
    }
</script>

<style lang='scss'>
    .autocomplete {
        position: relative;

        .results {
            /*font-size: 16px;*/
            max-height: 200px;
            position: absolute;
            left: 0;
            right: 0;
            background-color: white;
            border: solid 1px #ced4da;
            width: 100%;
            margin: 0px auto 0px;
            overflow-x: hidden;
            overflow-y: auto;
            z-index: 1;
            .item {
                cursor: pointer;
            }
        }
    }
</style>
