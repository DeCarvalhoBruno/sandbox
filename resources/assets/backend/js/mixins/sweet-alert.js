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
    },
    swalSaveWarning () {
      return swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
      })
    }
  }
}
