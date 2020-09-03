/*helper for axios*/
const transformFn = {
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    transformRequest: [(data, header) => {
        const formData = new FormData()
        Object.keys(data).forEach((key) => {
            formData.append(key, data[key])
        })
        return formData
    }]
}


/*Subscribers*/
export async function getSubscribers({
                                         cursor = 0,
                                         take = 50,
                                         sort = null,
                                         query = null,
                                         state = null
                                     } = {}) {
    let serverResponse = await axios.get(`${window.API_PATH}/subscribers`, {
        params: {
            cursor: cursor,
            take: take,
            sort: sort,
            query: query,
            state: state
        }
    });

    return serverResponse.data;
}

export async function deleteSubscriber(id = null) {
    if (id == null) {
        throw "Id must be provided";
    }

    let serverResponse = await axios.delete(`${window.API_PATH}/subscribers/${id}`, {headers: {'Content-Type': 'application/json'}});

    return serverResponse.data;
}

export async function getSubscriber(id = null) {
    if (id == null) {
        throw "Id must be provided";
    }

    let serverResponse = await axios.get(`${window.API_PATH}/subscribers/${id}`, {headers: {'Content-Type': 'application/json'}});

    return serverResponse.data;
}

export async function getSubscriberFields(id = null) {
    if (id == null) {
        throw "Id must be provided";
    }

    let serverResponse = await axios.get(`${window.API_PATH}/subscribers/${id}/fields`, {headers: {'Content-Type': 'application/json'}});

    return serverResponse.data;
}

export async function addSubscriber({
                                        email = '',
                                        name = '',
                                        state = ''
                                    } = {}) {

    let serverResponse = await axios.post(`${window.API_PATH}/subscribers`, {
            email: email,
            name: name,
            state: state
        },
        transformFn)

    return serverResponse.data;

}

export async function updateSubscriberState(id, state) {

    let serverResponse = await axios.post(`${window.API_PATH}/subscribers/${id}/state`, {
            state: state,
            _method: "PATCH"
        },
        transformFn)

    return serverResponse.data;

}

/*Fields*/
export async function getFields(query=null) {
    let serverResponse = await axios.get(`${window.API_PATH}/fields`,{
        params:{
            query:query
        }
    });

    return serverResponse.data;
}

export async function addField({
                                   title = '',
                                   type = '',
                                   description = '',
                                   default_value = null
                               } = {}) {

    let serverResponse = await axios.post(`${window.API_PATH}/fields`, {
            title: title,
            type: type,
            description: description,
            default_value: default_value
        },
        transformFn)

    return serverResponse.data;

}

export async function addFieldToSubscriber({
                                        subscriberId = 0,
                                        fieldId = 0,
                                        value = ''
                                    } = {}) {

    let serverResponse = await axios.post(`${window.API_PATH}/subscribers/${subscriberId}/fields`, {
            field_id:fieldId,
            value: value
        },
        transformFn)

    return serverResponse.data;

}

export default {getSubscribers, deleteSubscriber, addSubscriber, getFields, addFieldToSubscriber}