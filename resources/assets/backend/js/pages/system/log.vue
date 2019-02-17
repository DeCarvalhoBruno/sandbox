<template>
  <div class="row">
    <div class="col-md-12">
      <ul
          class="timeline"
          v-for="(period,index) in events" :key="index">
        <li class="time-label">
          <span class="label arrowed-in-right label-today">{{index}}</span>
        </li>
        <li v-for="(event, idx) in period" :key="index+idx">
          <fa :icon="event.icon" class="bg-blue"></fa>
          <div class="timeline-item">
            <span v-if="event.hasOwnProperty('date')">{{event.date}}</span>
            <span class="time">
              <fa icon="clock"
                  :class="['ml-2','mr-2']"></fa>
              {{event.time}}</span>
            <h3 class="timeline-header">{{event.title}}</h3>
            <div v-if="event.message.length"
                 class="timeline-body">
              <p v-for="(message, idx2) in event.message"
                 :key="index+idx+idx2">{{message}}</p>
            </div>
            <div v-show="event.message.length" class="timeline-footer">
            </div>
          </div>
        </li>
      </ul>
    </div>
  </div>
</template>
<script>
  import axios from 'axios'

  export default {
    layout: 'basic',
    middleware: 'check-auth',
    name: 'system-log',
    data () {
      return {
        events: []
      }
    },
    methods: {
      getInfo (data) {
        this.events = data.data.events
      },
      metaInfo () {
        return {title: this.$t('title.system_log')}
      }
    },
    beforeRouteEnter (to, from, next) {
      axios.get('/ajax/admin/system/events/log').then(({data}) => {
        next(vm => vm.getInfo({data}))
      })
    }
  }
</script>