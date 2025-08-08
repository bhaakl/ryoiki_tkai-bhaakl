<template>
  <div>
    <h3
      v-if="title"
      class="table-title"
      v-text="title"
    />

    <div class="table-default">
      <slot
        v-if="!hideHeader"
        name="thead"
      >
        <div class="table-row table-head">
          <div
            v-for="(header, index) in headers"
            :key="index"
            class="table-item"
          >
            <p
              js-tooltip
              :title="header.title"
              v-text="header.label"
            />
          </div>
        </div>
      </slot>

      <div
        v-if="$slots.prependItem"
        class="table-row"
      >
        <slot name="prependItem" />
      </div>

      <slot name="tbody">
        <div
          v-for="(row, rowIndex) in items"
          :key="rowIndex"
          class="table-row"
        >
          <div
            v-for="(header, cellIndex) in headers"
            :key="cellIndex"
            js-tooltip
            :title="header.title"
            class="table-item"
          >
            <slot
              :name="header.key"
              :row="row"
            >
              {{ row[header.key] }}
            </slot>
          </div>
        </div>
      </slot>

      <div
        v-if="$slots.appendItem"
        class="table-row"
      >
        <slot name="appendItem" />
      </div>

      <div
        v-if="!items.length"
        class="table-no-results"
      >
        <h4>Нет данных.</h4>
      </div>
    </div>

    <slot
      v-if="$slots.footer"
      name="footer"
    />
  </div>
</template>

<script>
export default {
  props: {
    loading: {
      type: Boolean,
      required: false
    },
    title: {
      type: String,
      default: null
    },
    headers: {
      type: Array,
      default: () => []
    },
    items: {
      type: Array,
      default: () => []
    },
    hideHeader: {
      type: Boolean,
      required: false
    },
    maxHeight: {
      type: String,
      default: '600px'
    }
  }
}
</script>

<style scoped lang="scss">
.table-title {
  font-family: NGEN Wide Medium, Helvetica, Arial, sans-serif;
  color: #001424;
  font-weight: 300;
  font-size: 20px;
}

.table-default {
  width: 100%;
  background: #fff;
  border-radius: 8px;
  margin: 10px 0 20px;
  display: table;
}

.table-row {
  display: table-row;
  border-bottom: 1px solid #f2f3f7;
  transition: all .25s ease-in-out;
}

.table-no-results {
  padding: 20px;
}

.table-row:first-child {
  border-top-left-radius: 8px;
  border-top-right-radius: 8px;
}

.table-head {
  background: #fff !important;
  cursor: default;
  font-weight: bold;
}

.table-item {
  display: table-cell;
  text-align: center;
  padding: 14px 20px;
  white-space: nowrap;
  align-items: center;
  overflow: hidden;
  text-overflow: ellipsis;
  border-right: 1px solid #f2f3f7;
  transition: background .25s ease-in-out, border .25s ease-in-out;
}

.table-head .table-item {
  padding: 14px 20px;
}

.table-item:first-child {
  //width: 25%;
  position: relative;
}

.table-head p {
  font-size: 12px;
  font-family: NGEN Compact Regular, Helvetica, Arial, sans-serif;
  color: #626c77;
}

.table-item p {
  font-size: 17px;
  line-height: 24px;
  color: #1d2023;
  font-family: NGEN Text Regular, Helvetica, Arial, sans-serif;
  transition: color .25s ease-in-out;
}
</style>
