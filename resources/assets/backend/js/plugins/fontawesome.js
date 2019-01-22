import Vue from 'vue'
import { library } from '@fortawesome/fontawesome-svg-core'
import {
  faUser,
  faLock,
  faSignOutAlt,
  faCog,
  faAngleDoubleUp,
  faAngleDoubleDown,
  faTrashAlt,
  faPencilAlt,
  faBars,
  faTachometerAlt,
  faStar,
  faKey,
  faAngleLeft,
  faArrowRight,
  faObjectGroup,
  faUsers,
  faCheck,
  faTimes,
  faExclamation,
  faCalendar,
  faCloudUploadAlt,
  faSyncAlt,
  faSync,
  faNewspaper,
  faMinus,
  faPlus,
  faPlusSquare,
  faBan,
  faSearch,
  faTag,
  faEnvelope
} from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

library.add(
  faUser, faLock, faSignOutAlt, faCog, faAngleDoubleUp, faAngleDoubleDown,
  faTrashAlt, faPencilAlt, faBars, faTachometerAlt, faStar, faKey, faAngleLeft,
  faArrowRight, faObjectGroup, faUsers, faCheck, faTimes, faExclamation,
  faCalendar, faCloudUploadAlt, faSyncAlt, faSync, faNewspaper, faMinus, faPlus,
  faPlusSquare, faBan, faSearch, faTag, faEnvelope
)

Vue.component('fa', FontAwesomeIcon)
