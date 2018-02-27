import Vue from 'vue'
import fontawesome from '@fortawesome/fontawesome'
import FontAwesomeIcon from '@fortawesome/vue-fontawesome'

Vue.component('fa', FontAwesomeIcon)

// import { } from '@fortawesome/fontawesome-free-regular/shakable.es'

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
  faUsers
} from '@fortawesome/fontawesome-free-solid/shakable.es'

fontawesome.library.add(
  faUser, faLock, faSignOutAlt, faCog, faAngleDoubleUp, faAngleDoubleDown,
  faTrashAlt, faPencilAlt, faBars, faTachometerAlt, faStar, faKey, faAngleLeft,
  faArrowRight,faObjectGroup,faUsers
)
