import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\AgencyDashboardController::exportMethod
* @see app/Http/Controllers/AgencyDashboardController.php:234
* @route '/admin/audit/export'
*/
export const exportMethod = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportMethod.url(options),
    method: 'get',
})

exportMethod.definition = {
    methods: ["get","head"],
    url: '/admin/audit/export',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\AgencyDashboardController::exportMethod
* @see app/Http/Controllers/AgencyDashboardController.php:234
* @route '/admin/audit/export'
*/
exportMethod.url = (options?: RouteQueryOptions) => {
    return exportMethod.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AgencyDashboardController::exportMethod
* @see app/Http/Controllers/AgencyDashboardController.php:234
* @route '/admin/audit/export'
*/
exportMethod.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportMethod.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AgencyDashboardController::exportMethod
* @see app/Http/Controllers/AgencyDashboardController.php:234
* @route '/admin/audit/export'
*/
exportMethod.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: exportMethod.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\AgencyDashboardController::exportMethod
* @see app/Http/Controllers/AgencyDashboardController.php:234
* @route '/admin/audit/export'
*/
const exportMethodForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportMethod.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AgencyDashboardController::exportMethod
* @see app/Http/Controllers/AgencyDashboardController.php:234
* @route '/admin/audit/export'
*/
exportMethodForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportMethod.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AgencyDashboardController::exportMethod
* @see app/Http/Controllers/AgencyDashboardController.php:234
* @route '/admin/audit/export'
*/
exportMethodForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportMethod.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

exportMethod.form = exportMethodForm

const audit = {
    export: Object.assign(exportMethod, exportMethod),
}

export default audit