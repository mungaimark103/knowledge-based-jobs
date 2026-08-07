import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\EmployerDashboardController::update
* @see app/Http/Controllers/EmployerDashboardController.php:138
* @route '/employer/organization'
*/
export const update = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(options),
    method: 'post',
})

update.definition = {
    methods: ["post"],
    url: '/employer/organization',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\EmployerDashboardController::update
* @see app/Http/Controllers/EmployerDashboardController.php:138
* @route '/employer/organization'
*/
update.url = (options?: RouteQueryOptions) => {
    return update.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\EmployerDashboardController::update
* @see app/Http/Controllers/EmployerDashboardController.php:138
* @route '/employer/organization'
*/
update.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\EmployerDashboardController::update
* @see app/Http/Controllers/EmployerDashboardController.php:138
* @route '/employer/organization'
*/
const updateForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\EmployerDashboardController::update
* @see app/Http/Controllers/EmployerDashboardController.php:138
* @route '/employer/organization'
*/
updateForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(options),
    method: 'post',
})

update.form = updateForm

const organization = {
    update: Object.assign(update, update),
}

export default organization