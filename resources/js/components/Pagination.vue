<template>
    <div class="row">
        <div class="col-md-12">
            <nav>
                <ul class="pagination">
                    <template v-if="pagination.current_page > 1">
                        <li class="page-item">
                            <a class="page-link" href="javascript:void(0)" rel="prev"
                                @click.prevent="changePage(pagination.current_page - 1)" aria-label="« Previous">‹</a>
                        </li>
                    </template>
                    <template v-else>
                        <li class="page-item disabled" aria-disabled="true" aria-label="« Previous">
                            <span class="page-link" aria-hidden="true">‹</span>
                        </li>
                    </template>

                    <template v-for="(item, index) in pagesNumber">
                        <template v-if="item == isActive">
                            <li class="page-item active" aria-current="page" :key="index">
                                <span class="page-link">
                                    {{ item }}
                                </span>
                            </li>
                        </template>
                        <template v-else>
                            <li class="page-item" :key="index" :class="item === '...' ? 'disabled' : ''">
                                <a class="page-link" href="javascript:void(0)" @click.prevent="changePage(item)">
                                    {{ item }}
                                </a>
                            </li>
                        </template>
                    </template>

                    <template v-if="(pagination.current_page < pagination.last_page)">
                        <li class="page-item">
                            <a class="page-link" href="javascript:void(0)"
                                @click.prevent="changePage(pagination.current_page + 1)" rel="next"
                                aria-label="Next »">›</a>
                        </li>
                    </template>
                    <template v-else>
                        <li class="page-item disabled" aria-disabled="true" aria-label="Next »">
                            <span class="page-link" aria-hidden="true">›</span>
                        </li>
                    </template>
                </ul>
            </nav>
        </div>
    </div>
</template>
<script>
export default {
    name: "Pagination",
    props: ['pagination'],
    computed: {
        isActive: function () {
            return this.pagination.current_page;
        },
        pagesNumber() {
            const { limit, total } = this.pagination;
            if (total == 0)
                return [];
            if (limit === -1) {
                return 0;
            }

            if (limit === 0) {
                return this.pagination.last_page;
            }

            var current = this.pagination.current_page;
            var last = this.pagination.last_page;
            var delta = this.pagination.limit;
            var left = current - delta;
            var right = current + delta + 1;
            var range = [];
            var pages = [];
            var l;

            for (var i = 1; i <= last; i++) {
                if (i === 1 || i === last || (i >= left && i < right)) {
                    range.push(i);
                }
            }
            range.forEach(function (i) {
                if (l) {
                    if (i - l === 2) {
                        pages.push(l + 1);
                    } else if (i - l !== 1) {
                        pages.push('...');
                    }
                }
                pages.push(i);
                l = i;
            });

            return pages;
        }
    },
    methods: {
        changePage(page) {
            if (page === "...")
                return;
            this.$emit("changePage", page);
        }
    }
}
</script>