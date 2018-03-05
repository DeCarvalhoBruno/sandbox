<script>
  export default {
    name: 'button-circle',
    props: {
      enabled: {
        type: Boolean,
        required: true
      },
      test:String
    },
    data () {
      return {
        buttonEnabled: this.enabled
      }
    },
    computed: {
      buttonTypeComputed () {
        return this.buttonEnabled ? 'btn-success' : 'btn-danger'
      },
      icon () {
        return this.buttonEnabled ? 'check' : 'times'
      }
    },
    methods: {
      toggle (e) {
        this.buttonEnabled = !this.buttonEnabled

        // this.$emit('buttonCircleClicked',this.buttonEnabled)
      }
    },
    render (h) {
      return h('button', {
        class: ['btn', 'btn-circle', this.buttonTypeComputed],
        on: {
          click: this.toggle
        },
        attrs: {
          type: 'button'
        }
      }, [
        h('fa', {props: {icon: this.icon}})
      ])
    }
  }
</script>