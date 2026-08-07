import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\AgencyDashboardController::provision
* @see app/Http/Controllers/AgencyDashboardController.php:209
* @route '/admin/staff/provision'
*/
export const provision = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: provision.url(options),
    method: 'post',
})

provision.definition = {
    methods: ["post"],
    url: '/admin/staff/provision',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AgencyDashboardController::provision
* @see app/Http/Controllers/AgencyDashboardController.php:209
* @route '/admin/staff/provision'
*/
provision.url = (options?: RouteQueryOptions) => {
    return provision.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AgencyDashboardController::provision
* @see app/Http/Controllers/AgencyDashboardController.php:209
* @route '/admin/staff/provision'
*/
provision.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: provision.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AgencyDashboardController::provision
* @see app/Http/Controllers/AgencyDashboardController.php:209
* @route '/admin/staff/provision'
*/
const provisionForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: provision.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AgencyDashboardController::provision
* @see app/Http/Controllers/AgencyDashboardController.php:209
* @route '/admin/staff/provision'
*/
provisionForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: provision.url(options),
    method: 'post',
})

provision.form = provisionForm

const staff = {
    provision: Object.assign(provision, provision),
}

export default staff