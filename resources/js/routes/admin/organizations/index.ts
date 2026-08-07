import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\AgencyDashboardController::verify
* @see app/Http/Controllers/AgencyDashboardController.php:121
* @route '/admin/organizations/{id}/verify'
*/
export const verify = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: verify.url(args, options),
    method: 'patch',
})

verify.definition = {
    methods: ["patch"],
    url: '/admin/organizations/{id}/verify',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\AgencyDashboardController::verify
* @see app/Http/Controllers/AgencyDashboardController.php:121
* @route '/admin/organizations/{id}/verify'
*/
verify.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
    }

    return verify.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AgencyDashboardController::verify
* @see app/Http/Controllers/AgencyDashboardController.php:121
* @route '/admin/organizations/{id}/verify'
*/
verify.patch = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: verify.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\AgencyDashboardController::verify
* @see app/Http/Controllers/AgencyDashboardController.php:121
* @route '/admin/organizations/{id}/verify'
*/
const verifyForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: verify.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AgencyDashboardController::verify
* @see app/Http/Controllers/AgencyDashboardController.php:121
* @route '/admin/organizations/{id}/verify'
*/
verifyForm.patch = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: verify.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

verify.form = verifyForm

const organizations = {
    verify: Object.assign(verify, verify),
}

export default organizations