<template>
    <v-datepicker :class="{'calendar-open-left':openLeft}"
                  ref="datePicker"
                  v-model="dateValue"
                  :name="name"
                  :language="language[$store.getters['lang/locale']]"
                  :monday-first="true"
                  :clear-button="showClearButton"
                  :format="$store.getters['lang/dateFormat']" @closed="closed">
        <!--<div slot="beforeCalendarHeader" class="calender-header">-->
        <!--<div class="container">-->
        <!--<div class="col-lg-12">-->
        <!--<div class="row justify-content-md-center"-->
        <!--id="datepicker-clear-button" v-show="showClearButton"><span>Clear</span></div>-->
        <!--<div class="row justify-content-md-center"-->
        <!--id="datepicker-today-button" v-show="showTodayButton"><span>Today</span></div>-->
        <!--</div>-->
        <!--</div>-->
        <!--</div>-->
    </v-datepicker>
</template>
<script>
  import Datepicker from 'vuejs-datepicker'
  import { en, fr } from 'vuejs-datepicker/dist/locale'

  export default {
    name: 'datepicker',
    components: {
      'v-datepicker': Datepicker
    },
    props: {
      showClearButton: {
        default: false,
        type: Boolean
      },
      showTodayButton: {
        default: false,
        type: Boolean
      },
      name: {
        type: String
      },
      value: {
        type: Date,
      },
      openLeft:{
        type:Boolean,
        default:false
      }
    },
    methods:{
      closed(){
        this.$emit('closed',this.dateValue)
      }
    },
    mounted(){
      this.dateValue = this.value
      this.$refs.datePicker.showCalendar()
      this.$refs.datePicker.$el.querySelector('input').focus()
    },
    data () {
      return {
        dateValue: '',
        language: {
          'en': en,
          'fr': fr
        }
      }
    },
    watch: {
      value (value) {
        this.dateValue = value
      },
    }
  }
</script>