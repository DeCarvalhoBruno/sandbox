export default {
  name: 'permission-mixin',
  methods: {
    getPermissions (permissions) {
      let permissionsLength = permissions.length
      let savedPermissions = {hasChanged: false}
      for (let i = 0; i < permissionsLength; i++) {
        let p = permissions[i].$attrs.entity
        if (!savedPermissions.hasOwnProperty(p)) {
          savedPermissions[p] = {mask: 0, hasChanged: false}
        }
        let currentlyEnabled = this.permissionIsCurrentlyEnabled(
          permissions[i].$el)
        if (currentlyEnabled) {
          savedPermissions[p].mask += permissions[i].$attrs.maskval
        }
        if (currentlyEnabled !== permissions[i].$attrs.hasPermission) {
          savedPermissions[p].hasChanged = true
          savedPermissions.hasChanged = true
        }
      }
      // console.log(savedPermissions)
      return savedPermissions
    },
    hasPermission (permissions, entity, type) {
      return (permissions.hasOwnProperty(entity) &&
        permissions[entity].hasOwnProperty(type))
    },
    togglePermission (val) {
      return val
    },
    permissionIsCurrentlyEnabled (el) {
      // class name is btn btn-circle btn-danger/btn-success
      return el.className.match(/d/) == null
    }
  }
}
