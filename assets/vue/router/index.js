import Vue from 'vue'
import Router from 'vue-router'
import store from "../store";

const Login = () => import('../views/Login');

const Container = () => import('../Container');
const Dashboard = () => import('../views/Dashboard');
const Boxes = () => import('../views/Boxes');

Vue.use(Router);

function configRoutes() {
    return [
        {
            path: '*',
            redirect: '/boxes',
            name: 'Home',
            component: Container,
            meta: { requiresAuth: true },
            children: [
                {
                    path: '/boxes',
                    name: 'Boxes',
                    component: Boxes
                },
                {
                    path: '/dashboard/:boxId',
                    name: 'Dashboard',
                    component: Dashboard
                },
            ]
        },
        {
            path: '/login',
            name: 'Login',
            component: Login
        }
    ]
}

let router = new Router({
    mode: 'hash',
    linkActiveClass: 'open active',
    scrollBehavior: () => ({y: 0}),
    routes: configRoutes()
});

export default router;

