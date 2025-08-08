<template>
  <div
    v-if="operations.length"
    :class="filteredOperations.length < 1 ? 'overflow-x-hidden' : 'overflow-x-auto'"
    class="rounded-2xl border border-solid border-divider/50"
  >
    <table class="tw-cashback-history-table">
      <thead>
        <tr>
          <th>Тип операции</th>
          <th>
            <div class="my-vc-calendar">
              <vc-date-picker
                v-model="range"
                mode="date"
                :popover="{ keepVisibleOnInput: true }"
                :masks="masks"
                :select-attribute="selectDragAttribute"
                :drag-attribute="selectDragAttribute"
                is-range
                title-position="left"
                :input-debounce="500"
                trim-weeks
                @drag="dragValue = $event"
              >
                <template #default="{ togglePopover }">
                  <div class="flex items-center">
                    <span class="whitespace-nowrap">Дата </span> <img
                      id="datepicker_btn"
                      ref="datepicker_btn"
                      class="cursor-pointer"
                      src="../../assets/icons/date-icon.svg"
                      alt=""
                      @click.stop="togglePopover()"
                    >
                  </div>
                </template>

                <template #day-popover="{ format }">
                  <div>
                    {{ format(dragValue ? dragValue.start : range.start, 'MMM D') }}
                    -
                    {{ format(dragValue ? dragValue.end : range.end, 'MMM D') }}
                  </div>
                </template>
                <template #footer>
                  <div class="datepicker-footer text-sm font-normal font-compact">
                    <div class="date-range">
                      <span>НАЧАЛО {{ formatDate(range.start) }}</span>
                      <span>КОНЕЦ {{ formatDate(range.end) }}</span>
                    </div>
                    <div class="actions">
                      <button
                        class="reset"
                        @click="resetRange"
                      >
                        СБРОСИТЬ
                      </button>
                      <button
                        class="apply"
                        @click="applyDateFilter"
                      >
                        ПРИМЕНИТЬ
                      </button>
                    </div>
                  </div>
                </template>
              </vc-date-picker>
            </div>
          </th>
          <th>Описание</th>
          <th>
            <div class="flex justify-center">
              <DropdownFilter
                class="py-0 px-0 my-0 mx-0"
                dropdown-type="cashback status"
                @checkedFilter="applyStatusFilter"
              />
            </div>
          </th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="(operation, index) in filteredOperations"
          :key="index"
          class="tw-tableitem-border"
        >
          <td>
            {{ operation.operation_type }} {{ operation.operation_value }}
          </td>
          <td>
            {{ formatDate(operation.created_at) }}
          </td>
          <td>
            {{ operation.description }}
          </td>
          <td>
            <div class="flex items-center justify-center gap-1.5">
              <div
                :class="{
                  'w-1.5 h-1.5 rounded-full': true,
                  'bg-positive': operation.status === 'accrued',
                  'bg-brand': operation.status === 'processing',
                }"
              />
              {{ getStatus(operation.status) }}
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  <table
    v-else
    class="tw-cashback-history-table"
  >
    <thead>
      <tr>
        <th>Тип операции</th>
        <th>
          <div class="my-vc-calendar">
            <vc-date-picker
              v-model="range"
              mode="date"
              :popover="{ keepVisibleOnInput: true }"
              :masks="masks"
              :select-attribute="selectDragAttribute"
              :drag-attribute="selectDragAttribute"
              is-range
              title-position="left"
              :input-debounce="500"
              trim-weeks
              @drag="dragValue = $event"
            >
              <template #default="{ togglePopover }">
                <div class="flex items-center">
                  <span class="whitespace-nowrap">Дата </span> <img
                    id="datepicker_btn"
                    ref="datepicker_btn"
                    class="cursor-pointer"
                    src="../../assets/icons/date-icon.svg"
                    alt=""
                    @click.stop="togglePopover()"
                  >
                </div>
              </template>

              <template #day-popover="{ format }">
                <div>
                  {{ format(dragValue ? dragValue.start : range.start, 'MMM D') }}
                  -
                  {{ format(dragValue ? dragValue.end : range.end, 'MMM D') }}
                </div>
              </template>
              <template #footer>
                <div class="datepicker-footer text-sm font-normal font-compact">
                  <div class="date-range">
                    <span>НАЧАЛО {{ formatDate(range.start) }}</span>
                    <span>КОНЕЦ {{ formatDate(range.end) }}</span>
                  </div>
                  <div class="actions">
                    <button
                      class="reset"
                      @click="resetRange"
                    >
                      СБРОСИТЬ
                    </button>
                    <button
                      class="apply"
                      @click="applyDateFilter"
                    >
                      ПРИМЕНИТЬ
                    </button>
                  </div>
                </div>
              </template>
            </vc-date-picker>
          </div>
        </th>
        <th>Описание</th>
        <th>
          <div class="flex justify-center">
            <DropdownFilter
              class="py-0 px-0 my-0 mx-0"
              dropdown-type="cashback status"
              @checkedFilter="applyStatusFilter"
            />
          </div>
        </th>
      </tr>
    </thead>
  </table>
</template>

<script>
import { DatePicker } from 'v-calendar'
import { isAfter, isWithinInterval, isSameDay, startOfDay, endOfDay } from 'date-fns'
import DropdownFilter from '@app/components/Shared/DropdownFilter.vue'
import { nextTick } from 'vue'

export default {
  components: {
    DropdownFilter,
    DatePicker
  },
  props: {
    operations: {
      type: Array,
      required: true,
      default: []
    }
  },
  data() {
    return {
      sortedOpsByDate: [],
      sortedOpsByStatus: [],
      lastDateFilterUpdate: null,
      lastStatusFilterUpdate: null,

      statusFilter: {
        processing: false,
        accrued: false
      },

      range: this.getDateRange(),
      dragValue: null,
      masks: {
        input: 'MMMM YYYY'
      }
    }
  },
  computed: {
    filteredOperations() {
      if (this.lastDateFilterUpdate > this.lastStatusFilterUpdate) {
        return this.sortedOpsByDate
      } else if (this.lastStatusFilterUpdate > this.lastDateFilterUpdate) {
        return this.sortedOpsByStatus
      } else {
        return this.operations
      }
    },
    selectDragAttribute() {
      return {
        highlight: {
          start: {
            fillMode: 'solid',
            style: {
              backgroundColor: '#1D2023'
            }
          },
          base: {
            fillMode: 'light',
            style: {
              backgroundColor: '#F2F3F7'
            }
          },
          end: {
            fillMode: 'solid',
            style: {
              backgroundColor: '#1D2023'
            }
          }
        },
        popover: {
          visibility: 'hover',
          isInteractive: false
        }
      }
    }
  },
  methods: {
    isBadDragVal() {
      if (!this.dragValue) {
        return true
      }

      return false
    },
    sortOperationsByStatus() {
      this.sortedOpsByStatus = this.operations.filter(operation => {
        if (!this.statusFilter.processing && !this.statusFilter.accrued) return true
        if (this.statusFilter.processing && operation.status === 'processing') return true
        if (this.statusFilter.accrued && operation.status === 'accrued') return true

        return false
      }).sort((a, b) => {
        // Сортировка по алфавиту, если оба фильтра активны
        if (this.statusFilter.processing && this.statusFilter.accrued) {
          return a.status.localeCompare(b.status)
        }

        return 0
      })
    },
    sortOperationsByDate() {
      if (!this.operations || this.operations.length === 0) {
        console.log('No operations to sort')

        return []
      }

      const startDate = (!this.isBadDragVal() && this.dragValue.start) ? this.dragValue.start : this.range.start
      const endDate = (!this.isBadDragVal() && this.dragValue.end) ? this.dragValue.end : this.range.end

      if (!startDate && !endDate) {
        return []
      }

      this.sortedOpsByDate = this.operations.filter(operation => {
        const operationDate = new Date(operation.created_at)
        console.log('operationDate--------', operationDate.toDateString())
        if (startDate && !endDate) {
          // Проверка только по начальной дате
          return isAfter(operationDate, startDate) || isSameDay(operationDate, startDate)
        } else if (startDate && endDate) {
          // Проверка по диапазону дат
          return isWithinInterval(operationDate, {
            start: startOfDay(startDate),
            end: endOfDay(endDate)
          })
        } else {
          // Если нет начальной даты, но есть конечная:
          return isBefore(operationDate, endDate) || isSameDay(operationDate, endDate)
        }
      }).sort((a, b) => new Date(a.date) - new Date(b.date))
    },
    applyStatusFilter(checks) {
      if (Array.isArray(checks)) {
        const set = new Set(checks)
        this.statusFilter.processing = set.has('processing')
        this.statusFilter.accrued = set.has('accrued')
        this.sortOperationsByStatus()
      }
      this.lastStatusFilterUpdate = Date.now()
    },
    applyDateFilter() {
      nextTick(() => {
        const button = this.$refs.datepicker_btn
        if (button) {
          button.click()
        }
      })
      this.sortOperationsByDate()
      this.lastDateFilterUpdate = Date.now()
    },
    resetRange() {
      this.range = this.getDateRange()
      this.sortedOpsByDate = []
    },
    getStatus(status) {
      switch (status) {
      case 'processing':
        return 'Обрабатывается'
      case 'accrued':
        return 'Начислено'
      }
    },
    formatDate(dateStr) {
      const date = new Date(dateStr)
      const day = String(date.getDate()).padStart(2, '0')
      const month = String(date.getMonth() + 1).padStart(2, '0')
      const year = date.getFullYear()

      return `${day}.${month}.${year}`
      // return date ? format(parseISO(date), 'dd.MM.yyyy') : ''
    },
    getDateRange() {
      const today = new Date()
      const fiveDaysLater = new Date(today)
      fiveDaysLater.setDate(today.getDate() + 5)

      if (fiveDaysLater.getMonth() !== today.getMonth()) {
        const fiveDaysBefore = new Date(today)
        fiveDaysBefore.setDate(today.getDate() - 5)

        return { start: fiveDaysBefore, end: today }
      } else {
        return { start: today, end: fiveDaysLater }
      }
    }
  }
}

</script>

<style scoped>
</style>
