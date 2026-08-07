import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\CandidateProfileController::update
* @see app/Http/Controllers/CandidateProfileController.php:89
* @route '/candidate/profile'
*/
export const update = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(options),
    method: 'post',
})

update.definition = {
    methods: ["post"],
    url: '/candidate/profile',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CandidateProfileController::update
* @see app/Http/Controllers/CandidateProfileController.php:89
* @route '/candidate/profile'
*/
update.url = (options?: RouteQueryOptions) => {
    return update.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CandidateProfileController::update
* @see app/Http/Controllers/CandidateProfileController.php:89
* @route '/candidate/profile'
*/
update.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CandidateProfileController::update
* @see app/Http/Controllers/CandidateProfileController.php:89
* @route '/candidate/profile'
*/
const updateForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CandidateProfileController::update
* @see app/Http/Controllers/CandidateProfileController.php:89
* @route '/candidate/profile'
*/
updateForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(options),
    method: 'post',
})

update.form = updateForm

const profile = {
    update: Object.assign(update, update),
}

export default profile