import {createRouter, createWebHistory} from 'vue-router';
import Home from './views/Home.vue'
import NotFound from './views/NotFound.vue'
import Dashboard from './views/Dashboard.vue'
import ReportError from './views/Report_Error.vue'
import Tasks from './views/Tasks.vue'
import Task from './views/Task.vue'

const routes = [
    {
        path: "/:catchAll(.*)",
        name: "NotFound",
        component: NotFound,
    },
    {
        path: '/',
        name: "Home",
        component: Home
    },
    {
        path: '/report-error',
        name: "ReportError",
        component: ReportError
    },
    {
        path: '/dashboard',
        name: "Dashboard",
        component: Dashboard,
        beforeEnter: validateAccessToken
    },
    {
        path: '/tasks',
        name: "Tasks",
        component: Tasks,
        beforeEnter: validateAccessToken
    },
    {
        path: '/task/:app_id/:carrier',
        name: "Task",
        component: Task,
        props: true,
        beforeEnter: validateAccessToken
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

async function validateAccessToken(to, from, next) {
    const accessToken = localStorage.getItem('token');

    if (!accessToken) {
        next({ name: "Home" });
        return;
    }

    await axios.post('/api/token/validate')
    .then(response => {
        if (response.data.valid) {
            to.meta.role = response.data.role;
            to.meta.type = response.data.type;
            next();
        } else {
            next({ name: "Home" });
        }
    })
    .catch(error => {
        console.error(error);
        next({ name: "Home" });
    });

    
}

export default router