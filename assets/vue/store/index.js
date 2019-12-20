import Vue from "vue";
import Vuex from "vuex";
import SecurityModule from "./security";
import UserModule from "./user";

Vue.use(Vuex);

export default new Vuex.Store({
    modules: {
        security: SecurityModule,
        user: UserModule,
    }
});
