import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\CandidateProfileController::view
* @see app/Http/Controllers/CandidateProfileController.php:57
* @route '/candidate/resume/view'
*/
export const view = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: view.url(options),
    method: 'get',
})

view.definition = {
    methods: ["get","head"],
    url: '/candidate/resume/view',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CandidateProfileController::view
* @see app/Http/Controllers/CandidateProfileController.php:57
* @route '/candidate/resume/view'
*/
view.url = (options?: RouteQueryOptions) => {
    return view.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CandidateProfileController::view
* @see app/Http/Controllers/CandidateProfileController.php:57
* @route '/candidate/resume/view'
*/
view.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: view.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CandidateProfileController::view
* @see app/Http/Controllers/CandidateProfileController.php:57
* @route '/candidate/resume/view'
*/
view.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: view.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\CandidateProfileController::view
* @see app/Http/Controllers/CandidateProfileController.php:57
* @route '/candidate/resume/view'
*/
const viewForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: view.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CandidateProfileController::view
* @see app/Http/Controllers/CandidateProfileController.php:57
* @route '/candidate/resume/view'
*/
viewForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: view.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CandidateProfileController::view
* @see app/Http/Controllers/CandidateProfileController.php:57
* @route '/candidate/resume/view'
*/
viewForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: view.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

view.form = viewForm

/**
* @see \App\Http\Controllers\CandidateProfileController::download
* @see app/Http/Controllers/CandidateProfileController.php:74
* @route '/candidate/resume/download'
*/
export const download = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: download.url(options),
    method: 'get',
})

download.definition = {
    methods: ["get","head"],
    url: '/candidate/resume/download',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CandidateProfileController::download
* @see app/Http/Controllers/CandidateProfileController.php:74
* @route '/candidate/resume/download'
*/
download.url = (options?: RouteQueryOptions) => {
    return download.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CandidateProfileController::download
* @see app/Http/Controllers/CandidateProfileController.php:74
* @route '/candidate/resume/download'
*/
download.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: download.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CandidateProfileController::download
* @see app/Http/Controllers/CandidateProfileController.php:74
* @route '/candidate/resume/download'
*/
download.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: download.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\CandidateProfileController::download
* @see app/Http/Controllers/CandidateProfileController.php:74
* @route '/candidate/resume/download'
*/
const downloadForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: download.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CandidateProfileController::download
* @see app/Http/Controllers/CandidateProfileController.php:74
* @route '/candidate/resume/download'
*/
downloadForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: download.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CandidateProfileController::download
* @see app/Http/Controllers/CandidateProfileController.php:74
* @route '/candidate/resume/download'
*/
downloadForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: download.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

download.form = downloadForm

const resume = {
    view: Object.assign(view, view),
    download: Object.assign(download, download),
}

export default resume