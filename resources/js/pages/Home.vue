<template>
    <div class="w-6/12 p-10 mx-auto">
        <div class="flex justify-between">
            <h1 class="text-2xl"> Blog </h1>
            <span class="capitalize">Welcome {{ user && user.name }}, 
                <button class="text-orange-500 underline px-5 py-2 hover:no-underline rounded-md" @click="handleLogout">Logout</button>
            </span>
        </div>
        <form @submit.prevent="addBlog" class="container-fluid">
            <div class="form-group">
                <input type="text" class="border px-5 py-2 rounded-md w-full mt-2" v-model="blog.title" placeholder="Blog title"/> 
            </div>
            <div class="form-group">
                <textarea class="border px-5 py-2 rounded-md w-full mt-2" v-model="blog.content" placeholder="Blog content"/>
            </div>
            <div class="form-group">
                <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded-md hover:bg-blue-400 w-full mt-2">{{blog_id?'Save' : 'Add'}}</button>
            </div>
        </form>
        <button @click="clearForm" class="bg-red-600 text-white px-5 py-2 rounded-md hover:bg-red-400 w-full mt-2">Cancel</button>
        <!-- <input type="text" class="p-2 w-64 border rounded-md" v-model="blog" placeholder="Enter your blog"/>
        <button class="bg-blue-600 text-white px-5 py-2 rounded-md ml-2 hover:bg-blue-400" @click="addBlog">Add</button> -->
        <Loader v-if="isLoading"/>
        <div class="card card-body border px-5 py-2 mt-2" v-for="item in blogs" v-bind:key="item.id">
            <h3><b>{{ item.title }}</b></h3>
            <p>{{ item.content }}</p>
            <div class="container-fluid flex justify-between">
                <button class="text-white px-5 py-2 rounded-md bg-blue-600" @click="showBlog(item.id)">Edit</button>
                <button class="text-white px-5 py-2 rounded-md bg-red-600" @click="deleteBlog(item.id)">Delete</button>   
            </div>
        </div>
        <nav class="card">
            <div class="container-fluid flex justify-between mt-2">
                <button v-bind:class="[{disabled: !pagination.prev_page_url}]" class="page-item border px-5 py-2 rounded-md mr-2" href="#" @click="fetchPagePrev"><a class="page-link">Previous</a></button>

                <span class="page-item disabled"><a class="page-link text-dark" href="#">Page {{ pagination.current_page }} of {{ pagination.last_page }}</a></span>
            
                <button v-bind:class="[{disabled: !pagination.next_page_url}]" class="page-item border px-5 py-2 rounded-md ml-2" href="#" @click="fetchPageNext"><a class="page-link" >Next</a></button>
            </div>
        </nav>

    </div>
</template>

<script>

    import {ref, onMounted} from 'vue'
    import {useRouter} from "vue-router";
    import {request} from '../helper'
    import Loader from '../components/Loader.vue';

    
    export default {
        components: {
            Loader,
        },
        setup() {
            const blog = ref({
                title: '',
                content: ''
            })
            const blogs = ref([])
            const user = ref()
            const isLoading = ref()
            const pagination = ref({})
            const isEdit = ref(false)
            const blog_id = ref()

            let router = useRouter();
            onMounted(() => {
                authentication()
                handleBlogs()
            });

            const authentication = async () => {
                isLoading.value = true
                try {
                    const req = await request('get', '/api/user')
                    user.value = req.data
                } catch (e) {
                    await router.push('/')
                }
            }

            const handleBlogs = async (page_url, message) => {
                // let vm = this
                try {
                    page_url = page_url || '/api/blogs'
                    const req = await request('get', page_url)
                    blogs.value = req.data.data
                    // console.log(req.data.links)
                    makePagination(req.data.meta, req.data.links)
                } catch (e) {
                    await router.push('/')
                    // console.log(e)
                }
                isLoading.value = false

                if(!!message){
                    return alert(message)
                }

            }

            const saveBlog = async (title, content) => {
                if(!isEdit.value){
                    try {
                        const data = {title: title, content: content}
                        const req = await request('post', '/api/blogs', data)
                        if (req.data.message) {
                            // isLoading.value = false
                            // alert(req.data.message)
                            handleBlogs('', req.data.message)
                        }
                        // blogs.value.push(req.data.data)
                    } catch (e) {
                        await router.push('/')
                    }
                }else{
                    try {
                        const data = {title: title, content: content}
                        const req = await request('put', `/api/blogs/${blog_id.value}`, data)
                        if (req.data.message) {
                            // isLoading.value = false
                            // alert(req.data.message)
                            handleBlogs('', req.data.message)
                        }
                        // blogs.value.push(req.data.data)
                    } catch (e) {
                        await router.push('/')
                    }
                }
                isLoading.value = false
            }

            const makePagination = (meta, links) => {
                let paginate = {
                    current_page: meta.current_page,
                    last_page: meta.last_page,
                    next_page_url: links.next,
                    prev_page_url: links.prev
                }

                pagination.value = paginate;
            }

            const showBlog = async (id)=> {
            
                try {
                    const req = await request('get', `/api/blogs/${id}`)
                    isEdit.value = true
                    blog_id.value = req.data.data.id
                    blog.value.title = req.data.data.title
                    blog.value.content = req.data.data.content
                    // makePagination(req.data.meta, req.data.links)
                } catch (e) {
                    await router.push('/')
                    // console.log(e)
                }
            }

            const handleLogout = () => {
                localStorage.removeItem('APP_DEMO_USER_TOKEN')
                router.push('/')
            }

            const addBlog = () => {
                // console.log(blog.value.title)
                if (blog.value.title === "" || blog.value.content === "") {
                    return alert("Blog title/content cannot be empty");
                }
                isLoading.value = true
                saveBlog(blog.value.title, blog.value.content)
                clearForm()
            }

            const fetchPageNext = () => {
                // console.log(blog.value.title)
                isLoading.value = true
                // console.log(pagination.value.next_page_url)
                handleBlogs(pagination.value.next_page_url)
            }

            const fetchPagePrev = () => {
                // console.log(blog.value.title)
                isLoading.value = true
                handleBlogs(pagination.value.prev_page_url)
            }

            const deleteBlog = async (id) => {
                if (window.confirm("Are you sure")) {
                    try {
                        const req = await request('delete', `/api/blogs/${id}`)
                        if (req.data.message) {
                            handleBlogs('', req.data.message)
                            // isLoading.value = false
                            // blogs.value.splice(index, 1)
                        }
                    } catch (e) {
                        await router.push('/')
                        // console.log(e)
                    }
                    isLoading.value = false
                }
            }

            const clearForm = () => {
                blog.value.title = ''
                blog.value.content = ''
                blog_id.value = ''
                isEdit.value = false
            }

            return {
                blog,
                blogs,
                user,
                addBlog,
                isLoading,
                deleteBlog,
                handleLogout,
                pagination,
                fetchPageNext,
                fetchPagePrev,
                handleBlogs,
                showBlog,
                isEdit,
                blog_id,
                clearForm,
                saveBlog
            }
        }
    }
</script>