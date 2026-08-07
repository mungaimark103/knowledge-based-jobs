import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\AgencyDashboardController::store
* @see app/Http/Controllers/AgencyDashboardController.php:278
* @route '/admin/rules'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/rules',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AgencyDashboardController::store
* @see app/Http/Controllers/AgencyDashboardController.php:278
* @route '/admin/rules'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AgencyDashboardController::store
* @see app/Http/Controllers/AgencyDashboardController.php:278
* @route '/admin/rules'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AgencyDashboardController::store
* @see app/Http/Controllers/AgencyDashboardController.php:278
* @route '/admin/rules'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AgencyDashboardController::store
* @see app/Http/Controllers/AgencyDashboardController.php:278
* @route '/admin/rules'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\AgencyDashboardController::toggle
* @see app/Http/Controllers/AgencyDashboardController.php:307
* @route '/admin/rules/{id}/toggle'
*/
export const toggle = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: toggle.url(args, options),
    method: 'patch',
})

toggle.definition = {
    methods: ["patch"],
    url: '/admin/rules/{id}/toggle',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\AgencyDashboardController::toggle
* @see app/Http/Controllers/AgencyDashboardController.php:307
* @route '/admin/rules/{id}/toggle'
*/
toggle.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return toggle.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AgencyDashboardController::toggle
* @see app/Http/Controllers/AgencyDashboardController.php:307
* @route '/admin/rules/{id}/toggle'
*/
toggle.patch = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: toggle.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\AgencyDashboardController::toggle
* @see app/Http/Controllers/AgencyDashboardController.php:307
* @route '/admin/rules/{id}/toggle'
*/
const toggleForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: toggle.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AgencyDashboardController::toggle
* @see app/Http/Controllers/AgencyDashboardController.php:307
* @route '/admin/rules/{id}/toggle'
*/
toggleForm.patch = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: toggle.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

toggle.form = toggleForm

const rules = {
    store: Object.assign(store, store),
    toggle: Object.assign(toggle, toggle),
}

export default rules