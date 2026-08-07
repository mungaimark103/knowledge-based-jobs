import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\EmployerDashboardController::store
* @see app/Http/Controllers/EmployerDashboardController.php:187
* @route '/employer/jobs'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/employer/jobs',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\EmployerDashboardController::store
* @see app/Http/Controllers/EmployerDashboardController.php:187
* @route '/employer/jobs'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\EmployerDashboardController::store
* @see app/Http/Controllers/EmployerDashboardController.php:187
* @route '/employer/jobs'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\EmployerDashboardController::store
* @see app/Http/Controllers/EmployerDashboardController.php:187
* @route '/employer/jobs'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\EmployerDashboardController::store
* @see app/Http/Controllers/EmployerDashboardController.php:187
* @route '/employer/jobs'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

const jobs = {
    store: Object.assign(store, store),
}

export default jobs