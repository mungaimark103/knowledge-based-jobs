import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\OpportunityController::index
* @see app/Http/Controllers/OpportunityController.php:19
* @route '/opportunities'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/opportunities',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\OpportunityController::index
* @see app/Http/Controllers/OpportunityController.php:19
* @route '/opportunities'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\OpportunityController::index
* @see app/Http/Controllers/OpportunityController.php:19
* @route '/opportunities'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\OpportunityController::index
* @see app/Http/Controllers/OpportunityController.php:19
* @route '/opportunities'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\OpportunityController::index
* @see app/Http/Controllers/OpportunityController.php:19
* @route '/opportunities'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\OpportunityController::index
* @see app/Http/Controllers/OpportunityController.php:19
* @route '/opportunities'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\OpportunityController::index
* @see app/Http/Controllers/OpportunityController.php:19
* @route '/opportunities'
*/
indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

/**
* @see \App\Http\Controllers\OpportunityController::show
* @see app/Http/Controllers/OpportunityController.php:94
* @route '/opportunities/{id}'
*/
export const show = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/opportunities/{id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\OpportunityController::show
* @see app/Http/Controllers/OpportunityController.php:94
* @route '/opportunities/{id}'
*/
show.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return show.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\OpportunityController::show
* @see app/Http/Controllers/OpportunityController.php:94
* @route '/opportunities/{id}'
*/
show.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\OpportunityController::show
* @see app/Http/Controllers/OpportunityController.php:94
* @route '/opportunities/{id}'
*/
show.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\OpportunityController::show
* @see app/Http/Controllers/OpportunityController.php:94
* @route '/opportunities/{id}'
*/
const showForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\OpportunityController::show
* @see app/Http/Controllers/OpportunityController.php:94
* @route '/opportunities/{id}'
*/
showForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\OpportunityController::show
* @see app/Http/Controllers/OpportunityController.php:94
* @route '/opportunities/{id}'
*/
showForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

show.form = showForm

/**
* @see \App\Http\Controllers\OpportunityController::apply
* @see app/Http/Controllers/OpportunityController.php:145
* @route '/opportunities/{id}/apply'
*/
export const apply = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: apply.url(args, options),
    method: 'post',
})

apply.definition = {
    methods: ["post"],
    url: '/opportunities/{id}/apply',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\OpportunityController::apply
* @see app/Http/Controllers/OpportunityController.php:145
* @route '/opportunities/{id}/apply'
*/
apply.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return apply.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\OpportunityController::apply
* @see app/Http/Controllers/OpportunityController.php:145
* @route '/opportunities/{id}/apply'
*/
apply.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: apply.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\OpportunityController::apply
* @see app/Http/Controllers/OpportunityController.php:145
* @route '/opportunities/{id}/apply'
*/
const applyForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: apply.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\OpportunityController::apply
* @see app/Http/Controllers/OpportunityController.php:145
* @route '/opportunities/{id}/apply'
*/
applyForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: apply.url(args, options),
    method: 'post',
})

apply.form = applyForm

/**
* @see \App\Http\Controllers\OpportunityController::applicants
* @see app/Http/Controllers/OpportunityController.php:228
* @route '/opportunities/{id}/applicants'
*/
export const applicants = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: applicants.url(args, options),
    method: 'get',
})

applicants.definition = {
    methods: ["get","head"],
    url: '/opportunities/{id}/applicants',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\OpportunityController::applicants
* @see app/Http/Controllers/OpportunityController.php:228
* @route '/opportunities/{id}/applicants'
*/
applicants.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return applicants.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\OpportunityController::applicants
* @see app/Http/Controllers/OpportunityController.php:228
* @route '/opportunities/{id}/applicants'
*/
applicants.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: applicants.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\OpportunityController::applicants
* @see app/Http/Controllers/OpportunityController.php:228
* @route '/opportunities/{id}/applicants'
*/
applicants.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: applicants.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\OpportunityController::applicants
* @see app/Http/Controllers/OpportunityController.php:228
* @route '/opportunities/{id}/applicants'
*/
const applicantsForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: applicants.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\OpportunityController::applicants
* @see app/Http/Controllers/OpportunityController.php:228
* @route '/opportunities/{id}/applicants'
*/
applicantsForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: applicants.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\OpportunityController::applicants
* @see app/Http/Controllers/OpportunityController.php:228
* @route '/opportunities/{id}/applicants'
*/
applicantsForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: applicants.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

applicants.form = applicantsForm

const opportunities = {
    index: Object.assign(index, index),
    show: Object.assign(show, show),
    apply: Object.assign(apply, apply),
    applicants: Object.assign(applicants, applicants),
}

export default opportunities