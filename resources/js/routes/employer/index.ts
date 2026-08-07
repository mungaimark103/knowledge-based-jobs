import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
import portal from './portal'
import organization from './organization'
import jobs from './jobs'
/**
* @see \App\Http\Controllers\EmployerDashboardController::dashboard
* @see app/Http/Controllers/EmployerDashboardController.php:56
* @route '/employer/dashboard'
*/
export const dashboard = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dashboard.url(options),
    method: 'get',
})

dashboard.definition = {
    methods: ["get","head"],
    url: '/employer/dashboard',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\EmployerDashboardController::dashboard
* @see app/Http/Controllers/EmployerDashboardController.php:56
* @route '/employer/dashboard'
*/
dashboard.url = (options?: RouteQueryOptions) => {
    return dashboard.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\EmployerDashboardController::dashboard
* @see app/Http/Controllers/EmployerDashboardController.php:56
* @route '/employer/dashboard'
*/
dashboard.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dashboard.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\EmployerDashboardController::dashboard
* @see app/Http/Controllers/EmployerDashboardController.php:56
* @route '/employer/dashboard'
*/
dashboard.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: dashboard.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\EmployerDashboardController::dashboard
* @see app/Http/Controllers/EmployerDashboardController.php:56
* @route '/employer/dashboard'
*/
const dashboardForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: dashboard.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\EmployerDashboardController::dashboard
* @see app/Http/Controllers/EmployerDashboardController.php:56
* @route '/employer/dashboard'
*/
dashboardForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: dashboard.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\EmployerDashboardController::dashboard
* @see app/Http/Controllers/EmployerDashboardController.php:56
* @route '/employer/dashboard'
*/
dashboardForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: dashboard.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

dashboard.form = dashboardForm

const employer = {
    portal: Object.assign(portal, portal),
    dashboard: Object.assign(dashboard, dashboard),
    organization: Object.assign(organization, organization),
    jobs: Object.assign(jobs, jobs),
}

export default employer