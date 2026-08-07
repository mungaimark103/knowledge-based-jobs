import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\CandidateProfileController::uploadResume
* @see app/Http/Controllers/CandidateProfileController.php:18
* @route '/candidate/resume'
*/
export const uploadResume = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: uploadResume.url(options),
    method: 'post',
})

uploadResume.definition = {
    methods: ["post"],
    url: '/candidate/resume',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CandidateProfileController::uploadResume
* @see app/Http/Controllers/CandidateProfileController.php:18
* @route '/candidate/resume'
*/
uploadResume.url = (options?: RouteQueryOptions) => {
    return uploadResume.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CandidateProfileController::uploadResume
* @see app/Http/Controllers/CandidateProfileController.php:18
* @route '/candidate/resume'
*/
uploadResume.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: uploadResume.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CandidateProfileController::uploadResume
* @see app/Http/Controllers/CandidateProfileController.php:18
* @route '/candidate/resume'
*/
const uploadResumeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: uploadResume.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CandidateProfileController::uploadResume
* @see app/Http/Controllers/CandidateProfileController.php:18
* @route '/candidate/resume'
*/
uploadResumeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: uploadResume.url(options),
    method: 'post',
})

uploadResume.form = uploadResumeForm

/**
* @see \App\Http\Controllers\CandidateProfileController::updateStructuredProfile
* @see app/Http/Controllers/CandidateProfileController.php:89
* @route '/candidate/profile'
*/
export const updateStructuredProfile = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateStructuredProfile.url(options),
    method: 'post',
})

updateStructuredProfile.definition = {
    methods: ["post"],
    url: '/candidate/profile',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CandidateProfileController::updateStructuredProfile
* @see app/Http/Controllers/CandidateProfileController.php:89
* @route '/candidate/profile'
*/
updateStructuredProfile.url = (options?: RouteQueryOptions) => {
    return updateStructuredProfile.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CandidateProfileController::updateStructuredProfile
* @see app/Http/Controllers/CandidateProfileController.php:89
* @route '/candidate/profile'
*/
updateStructuredProfile.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateStructuredProfile.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CandidateProfileController::updateStructuredProfile
* @see app/Http/Controllers/CandidateProfileController.php:89
* @route '/candidate/profile'
*/
const updateStructuredProfileForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateStructuredProfile.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CandidateProfileController::updateStructuredProfile
* @see app/Http/Controllers/CandidateProfileController.php:89
* @route '/candidate/profile'
*/
updateStructuredProfileForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateStructuredProfile.url(options),
    method: 'post',
})

updateStructuredProfile.form = updateStructuredProfileForm

/**
* @see \App\Http\Controllers\CandidateProfileController::viewResume
* @see app/Http/Controllers/CandidateProfileController.php:57
* @route '/candidate/resume/view'
*/
export const viewResume = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: viewResume.url(options),
    method: 'get',
})

viewResume.definition = {
    methods: ["get","head"],
    url: '/candidate/resume/view',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CandidateProfileController::viewResume
* @see app/Http/Controllers/CandidateProfileController.php:57
* @route '/candidate/resume/view'
*/
viewResume.url = (options?: RouteQueryOptions) => {
    return viewResume.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CandidateProfileController::viewResume
* @see app/Http/Controllers/CandidateProfileController.php:57
* @route '/candidate/resume/view'
*/
viewResume.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: viewResume.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CandidateProfileController::viewResume
* @see app/Http/Controllers/CandidateProfileController.php:57
* @route '/candidate/resume/view'
*/
viewResume.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: viewResume.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\CandidateProfileController::viewResume
* @see app/Http/Controllers/CandidateProfileController.php:57
* @route '/candidate/resume/view'
*/
const viewResumeForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: viewResume.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CandidateProfileController::viewResume
* @see app/Http/Controllers/CandidateProfileController.php:57
* @route '/candidate/resume/view'
*/
viewResumeForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: viewResume.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CandidateProfileController::viewResume
* @see app/Http/Controllers/CandidateProfileController.php:57
* @route '/candidate/resume/view'
*/
viewResumeForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: viewResume.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

viewResume.form = viewResumeForm

/**
* @see \App\Http\Controllers\CandidateProfileController::downloadResume
* @see app/Http/Controllers/CandidateProfileController.php:74
* @route '/candidate/resume/download'
*/
export const downloadResume = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: downloadResume.url(options),
    method: 'get',
})

downloadResume.definition = {
    methods: ["get","head"],
    url: '/candidate/resume/download',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CandidateProfileController::downloadResume
* @see app/Http/Controllers/CandidateProfileController.php:74
* @route '/candidate/resume/download'
*/
downloadResume.url = (options?: RouteQueryOptions) => {
    return downloadResume.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CandidateProfileController::downloadResume
* @see app/Http/Controllers/CandidateProfileController.php:74
* @route '/candidate/resume/download'
*/
downloadResume.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: downloadResume.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CandidateProfileController::downloadResume
* @see app/Http/Controllers/CandidateProfileController.php:74
* @route '/candidate/resume/download'
*/
downloadResume.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: downloadResume.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\CandidateProfileController::downloadResume
* @see app/Http/Controllers/CandidateProfileController.php:74
* @route '/candidate/resume/download'
*/
const downloadResumeForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: downloadResume.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CandidateProfileController::downloadResume
* @see app/Http/Controllers/CandidateProfileController.php:74
* @route '/candidate/resume/download'
*/
downloadResumeForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: downloadResume.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CandidateProfileController::downloadResume
* @see app/Http/Controllers/CandidateProfileController.php:74
* @route '/candidate/resume/download'
*/
downloadResumeForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: downloadResume.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

downloadResume.form = downloadResumeForm

const CandidateProfileController = { uploadResume, updateStructuredProfile, viewResume, downloadResume }

export default CandidateProfileController