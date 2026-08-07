import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\AgencyDashboardController::verify
* @see app/Http/Controllers/AgencyDashboardController.php:139
* @route '/admin/candidates/{id}/verify'
*/
export const verify = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: verify.url(args, options),
    method: 'patch',
})

verify.definition = {
    methods: ["patch"],
    url: '/admin/candidates/{id}/verify',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\AgencyDashboardController::verify
* @see app/Http/Controllers/AgencyDashboardController.php:139
* @route '/admin/candidates/{id}/verify'
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
* @see app/Http/Controllers/AgencyDashboardController.php:139
* @route '/admin/candidates/{id}/verify'
*/
verify.patch = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: verify.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\AgencyDashboardController::verify
* @see app/Http/Controllers/AgencyDashboardController.php:139
* @route '/admin/candidates/{id}/verify'
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
* @see app/Http/Controllers/AgencyDashboardController.php:139
* @route '/admin/candidates/{id}/verify'
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

/**
* @see \App\Http\Controllers\AgencyDashboardController::proxy
* @see app/Http/Controllers/AgencyDashboardController.php:159
* @route '/admin/candidates/proxy'
*/
export const proxy = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: proxy.url(options),
    method: 'post',
})

proxy.definition = {
    methods: ["post"],
    url: '/admin/candidates/proxy',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AgencyDashboardController::proxy
* @see app/Http/Controllers/AgencyDashboardController.php:159
* @route '/admin/candidates/proxy'
*/
proxy.url = (options?: RouteQueryOptions) => {
    return proxy.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AgencyDashboardController::proxy
* @see app/Http/Controllers/AgencyDashboardController.php:159
* @route '/admin/candidates/proxy'
*/
proxy.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: proxy.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AgencyDashboardController::proxy
* @see app/Http/Controllers/AgencyDashboardController.php:159
* @route '/admin/candidates/proxy'
*/
const proxyForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: proxy.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AgencyDashboardController::proxy
* @see app/Http/Controllers/AgencyDashboardController.php:159
* @route '/admin/candidates/proxy'
*/
proxyForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: proxy.url(options),
    method: 'post',
})

proxy.form = proxyForm

const candidates = {
    verify: Object.assign(verify, verify),
    proxy: Object.assign(proxy, proxy),
}

export default candidates