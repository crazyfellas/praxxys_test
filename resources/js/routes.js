import Product from './components/Product';
import CreateProduct from './components/CreateProduct';
import EditProduct from './components/EditProduct';

export default{
    mode: 'history',
    linkActiveClass: 'font-semibold',

    routes: [
        {
            path: '/',
            component: Product,
            name: "products"
        },
       
        {
            path: '/products',
            component: Product,
            name: "products"
        },
        {
            path: '/CreateProduct',
            component: CreateProduct
        },
        {
            path: '/EditProduct/:id',
            component: EditProduct,
            name: "edit"
        }
    ]
}