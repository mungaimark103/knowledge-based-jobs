import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\EmployerDashboardController::switchMethod
* @see app/Http/Controllers/EmployerDashboardController.php:21
* @route '/employer/portal-switch'
*/
export const switchMethod = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: switchMethod.url(options),
    method: 'get',
})

switchMethod.definition = {
    methods: ["get","head"],
    url: '/employer/portal-switch',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\EmployerDashboardController::switchMethod
* @see app/Http/Controllers/EmployerDashboardController.php:21
* @route '/employer/portal-switch'
*/
switchMethod.url = (options?: RouteQueryOptions) => {
    return switchMethod.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\EmployerDashboardController::switchMethod
* @see app/Http/Controllers/EmployerDashboardController.php:21
* @route '/employer/portal-switch'
*/
switchMethod.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: switchMethod.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\EmployerDashboardController::switchMethod
* @see app/Http/Controllers/EmployerDashboardController.php:21
* @route '/employer/portal-switch'
*/
switchMethod.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: switchMethod.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\EmployerDashboardController::switchMethod
* @see app/Http/Controllers/EmployerDashboardController.php:21
* @route '/employer/portal-switch'
*/
const switchMethodForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: switchMethod.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\EmployerDashboardController::switchMethod
* @see app/Http/Controllers/EmployerDashboardController.php:21
* @route '/employer/portal-switch'
*/
switchMethodForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: switchMethod.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\EmployerDashboardController::switchMethod
* @see app/Http/Controllers/EmployerDashboardController.php:21
* @route '/employer/portal-switch'
*/
switchMethodForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: switchMethod.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

switchMethod.form = switchMethodForm

const portal = {
    switch: Object.assign(switchMethod, switchMethod),
}

export default portal