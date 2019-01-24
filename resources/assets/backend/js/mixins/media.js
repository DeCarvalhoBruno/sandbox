export default {
  name: 'media',
  methods: {
    getImageUrl (uuid, suffix, ext) {
      if (typeof uuid === 'undefined') {
        return null
      }
      let string = `/media/${this.type}/${this.media}/${uuid}`
      if (suffix !== null) {
        string += `_${suffix}.${ext}`
      } else {
        string += `.${ext}`
      }
      return string
    }
  }
}
