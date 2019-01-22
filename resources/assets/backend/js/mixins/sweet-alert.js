import swal from 'sweetalert2'

export default {
  name: 'swal',
  methods: {
    swalInit () {
      return swal.mixin({
        position: 'top-end',
        toast: true
      })
    },
    swalNotification (type, title) {
      this.swalInit().fire({
        type: type,
        title: title,
        showConfirmButton: false,
        timer: 3000
      })
    }
  }
}
