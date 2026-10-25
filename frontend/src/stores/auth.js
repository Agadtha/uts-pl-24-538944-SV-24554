import { reactive } from 'vue'

const TOKEN_KEY = 'gudang_token'
const USER_KEY = 'gudang_user'

function readUser() {
  try {
    return JSON.parse(localStorage.getItem(USER_KEY) || 'null')
  } catch {
    return null
  }
}


export const auth = reactive({
  token: localStorage.getItem(TOKEN_KEY) || '',
  user: readUser(),

  set(token, user) {
    this.token = token
    this.user = user
    localStorage.setItem(TOKEN_KEY, token)
    localStorage.setItem(USER_KEY, JSON.stringify(user))
  },

  clear() {
    this.token = ''
    this.user = null
    localStorage.removeItem(TOKEN_KEY)
    localStorage.removeItem(USER_KEY)
  },
})
