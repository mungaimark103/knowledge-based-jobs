import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\AgencyDashboardController::weights
* @see app/Http/Controllers/AgencyDashboardController.php:194
* @route '/admin/criteria/weights'
*/
export const weights = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: weights.url(options),
    method: 'post',
})

weights.definition = {
    methods: ["post"],
    url: '/admin/criteria/weights',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AgencyDashboardController::weights
* @see app/Http/Controllers/AgencyDashboardController.php:194
* @route '/admin/criteria/weights'
*/
weights.url = (options?: RouteQueryOptions) => {
    return weights.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AgencyDashboardController::weights
* @see app/Http/Controllers/AgencyDashboardController.php:194
* @route '/admin/criteria/weights'
*/
weights.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: weights.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AgencyDashboardController::weights
* @see app/Http/Controllers/AgencyDashboardController.php:194
* @route '/admin/criteria/weights'
*/
const weightsForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: weights.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AgencyDashboardController::weights
* @see app/Http/Controllers/AgencyDashboardController.php:194
* @route '/admin/criteria/weights'
*/
weightsForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: weights.url(options),
    method: 'post',
})

weights.form = weightsForm

const criteria = {
    weights: Object.assign(weights, weights),
}

export default criteria